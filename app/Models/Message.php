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
        'user_id',
        'parent_id',
        'read_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'is_read' => false,
        'type' => 'contact_form',
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
     * Get thread messages (all messages in the same conversation).
     */
    public function getThreadMessages()
    {
        // Check if parent_id column exists before using it
        if (!$this->getConnection()->getSchemaBuilder()->hasColumn('messages', 'parent_id')) {
            return collect(); // Return empty collection if column doesn't exist
        }

        // If this message has a parent, get the root message
        $rootMessage = $this->parent_id ? $this->parent : $this;
        
        // Get all messages in this thread (root + all its replies)
        return Message::where('id', $rootMessage->id)
                     ->orWhere('parent_id', $rootMessage->id)
                     ->orderBy('created_at');
    }

    /**
     * Get the root message of this thread.
     */
    public function getRootMessage()
    {
        return $this->parent_id ? $this->parent : $this;
    }

    /**
     * Check if this message is a reply.
     */
    public function isReply()
    {
        return !is_null($this->parent_id ?? null);
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
    }
}