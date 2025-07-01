<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'subject',
        'message',
        'type',
        'is_read',
        'is_replied',
        'user_id',
        'parent_id',
        'project_id',  // <- Add this if not already present
        'priority',    // <- Add this if not already present
        'read_at',
        'replied_at',
        'replied_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_read' => 'boolean',
        'is_replied' => 'boolean',
        'read_at' => 'datetime',
        'replied_at' => 'datetime',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'is_read' => false,
        'is_replied' => false,
        'type' => 'contact_form',
        'priority' => 'normal',  // <- Add this default
    ];

    /**
     * Get the user that owns the message (if it's from a registered user).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the client that owns the message (alias for user relationship).
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the project associated with this message.
     * 
     * ADD THIS RELATIONSHIP - This is what was missing!
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'client_id');
    }

    /**
     * Get the admin user who replied to this message.
     */
    public function repliedBy()
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    /**
     * Get the parent message (for replies).
     */
    public function parent()
    {
        return $this->belongsTo(Message::class, 'parent_id');
    }

    /**
     * Get the child messages (replies to this message).
     */
    public function replies()
    {
        return $this->hasMany(Message::class, 'parent_id')->orderBy('created_at');
    }

    /**
     * Get all attachments for this message.
     */
    public function attachments()
    {
        return $this->hasMany(MessageAttachment::class);
    }

    /**
     * Scope a query to only include unread messages.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope a query to only include read messages.
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope a query to only include unreplied messages.
     */
    public function scopeUnreplied($query)
    {
        return $query->where('is_replied', false);
    }

    /**
     * Scope a query to only include replied messages.
     */
    public function scopeReplied($query)
    {
        return $query->where('is_replied', true);
    }

    /**
     * Scope a query to exclude admin messages from listings.
     */
    public function scopeExcludeAdminMessages($query)
    {
        return $query->whereNotIn('type', ['admin_to_client']);
    }

    /**
     * Scope a query to only include client messages.
     */
    public function scopeClientMessages($query)
    {
        return $query->whereIn('type', ['client_to_admin', 'contact_form']);
    }

    /**
     * Scope a query to only include admin messages.
     */
    public function scopeAdminMessages($query)
    {
        return $query->where('type', 'admin_to_client');
    }

    /**
     * Scope a query to search messages.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('subject', 'like', "%{$search}%")
                ->orWhere('message', 'like', "%{$search}%");
        });
    }

    /**
     * Scope a query to filter by project.
     * 
     * ADD THIS SCOPE - For filtering messages by project
     */
    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * Scope a query to filter by priority.
     * 
     * ADD THIS SCOPE - For filtering messages by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Get thread messages (all messages in the same conversation).
     */
    public function getThreadMessages()
    {
        // Get the root message
        $rootMessage = $this->parent_id ? $this->parent : $this;
        
        // Get all messages in this thread
        return Message::where(function($query) use ($rootMessage) {
            $query->where('id', $rootMessage->id)
                ->orWhere('parent_id', $rootMessage->id);
        })->orderBy('created_at');
    }
    public function getCompleteThread()
    {
        return $this->getThreadMessages()->with(['attachments', 'user'])->get();
    }
    public function getThreadStats()
    {
        $thread = $this->getCompleteThread();
        
        return [
            'total_messages' => $thread->count(),
            'client_messages' => $thread->where('type', '!=', 'admin_to_client')->count(),
            'admin_messages' => $thread->where('type', 'admin_to_client')->count(),
            'has_unread' => $thread->where('is_read', false)->count() > 0,
            'last_activity' => $thread->max('created_at'),
        ];
    }

    public function isRootMessage()
    {
        return is_null($this->parent_id);
    }

    /**
     * Check if this message is a reply.
     */
    public function isReply()
    {
        return !is_null($this->parent_id ?? null);
    }

    /**
     * Check if this message is from admin.
     */
    public function isFromAdmin()
    {
        return $this->type === 'admin_to_client';
    }

    /**
     * Check if this message is from client.
     */
    public function isFromClient()
    {
        return in_array($this->type, ['client_to_admin', 'contact_form']);
    }

    /**
     * Check if this message has been replied to.
     */
    public function hasBeenReplied()
    {
        return $this->is_replied;
    }

    /**
     * Check if message is urgent.
     * 
     * ADD THIS METHOD - For checking message urgency
     */
    public function isUrgent()
    {
        return $this->priority === 'urgent';
    }

    /**
     * Check if message is related to a project.
     * 
     * ADD THIS METHOD - For checking if message has project context
     */
    public function hasProject()
    {
        return !is_null($this->project_id) && $this->project()->exists();
    }

    /**
     * Mark message as read.
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Mark message as unread.
     */
    public function markAsUnread()
    {
        if ($this->is_read) {
            $this->update([
                'is_read' => false,
                'read_at' => null,
            ]);
        }
    }

    /**
     * Mark message as replied.
     */
    public function markAsReplied($repliedBy = null)
    {
        if (!$this->is_replied) {
            $this->update([
                'is_replied' => true,
                'replied_at' => now(),
                'replied_by' => $repliedBy ?: auth()->id(),
            ]);
        }
    }

    /**
     * Mark message as unreplied.
     */
    public function markAsUnreplied()
    {
        if ($this->is_replied) {
            $this->update([
                'is_replied' => false,
                'replied_at' => null,
                'replied_by' => null,
            ]);
        }
    }

    /**
     * Toggle read status.
     */
    public function toggleReadStatus()
    {
        if ($this->is_read) {
            $this->markAsUnread();
        } else {
            $this->markAsRead();
        }
    }

    /**
     * Get status priority for sorting (unreplied and unread are higher priority).
     */
    public function getStatusPriorityAttribute()
    {
        if (!$this->is_replied && !$this->is_read) {
            return 1; // Highest priority - unreplied and unread
        } elseif (!$this->is_replied) {
            return 2; // High priority - unreplied but read
        } elseif (!$this->is_read) {
            return 3; // Medium priority - replied but unread
        } else {
            return 4; // Lowest priority - replied and read
        }
    }
    public function getStatusColorAttribute()
    {
        switch ($this->display_status) {
            case 'urgent':
                return 'text-red-600 bg-red-100';
            case 'unread':
                return 'text-blue-600 bg-blue-100';
            case 'replied':
                return 'text-green-600 bg-green-100';
            default:
                return 'text-gray-600 bg-gray-100';
        }
    }

    /**
     * Get the formatted date attribute.
     */
    public function getDateFormattedAttribute()
    {
        return $this->created_at->format('M d, Y H:i');
    }

    /**
     * Get the short message attribute.
     */
    public function getShortMessageAttribute()
    {
        return \Illuminate\Support\Str::limit(strip_tags($this->message), 100);
    }

    /**
     * Get priority badge color.
     * 
     * ADD THIS METHOD - For UI display of priority
     */
    public function getPriorityColorAttribute()
    {
        return match ($this->priority) {
            'urgent' => 'red',
            'high' => 'orange',
            'normal' => 'blue',
            'low' => 'gray',
            default => 'gray'
        };
    }

    /**
     * Get formatted priority name.
     * 
     * ADD THIS METHOD - For UI display of priority
     */
    public function getFormattedPriorityAttribute()
    {
        return match ($this->priority) {
            'urgent' => 'Urgent',
            'high' => 'High',
            'normal' => 'Normal',
            'low' => 'Low',
            default => 'Normal'
        };
    }

    /**
     * Save a new message attachment.
     */
    public function addAttachment($file)
    {
        $path = $file->store('message-attachments', 'public');

        return $this->attachments()->create([
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'file_type' => $file->getMimeType(),
        ]);
    }

    /**
     * Get human readable file size.
     */
    public static function humanFileSize($bytes, $decimals = 2)
    {
        $size = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Clean up attachments when message is deleted
        static::deleting(function ($message) {
            $message->attachments()->each(function ($attachment) {
                $attachment->delete(); // This will trigger the attachment's deleting event
            });
        });

        // Auto-mark parent message as replied when admin creates a reply
        static::created(function ($message) {
            if ($message->type === 'admin_to_client' && $message->parent_id) {
                $parentMessage = Message::find($message->parent_id);
                if ($parentMessage && !$parentMessage->is_replied) {
                    $parentMessage->markAsReplied($message->replied_by ?: auth()->id());
                }
            }
        });
    }
}