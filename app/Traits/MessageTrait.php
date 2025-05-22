<?php

namespace App\Traits;

use App\Models\MessageAttachment;

trait MessageTrait
{
    /**
     * Boot the trait.
     */
    protected static function bootMessageTrait()
    {
        // Clean up attachments when message is deleted
        static::deleting(function ($message) {
            $message->attachments()->each(function ($attachment) {
                $attachment->delete(); // This will trigger the attachment's deleting event
            });
        });
    }

    /**
     * Get all attachments for this message.
     */
    public function attachments()
    {
        return $this->hasMany(MessageAttachment::class);
    }

    /**
     * Get the user that owns the message (if it's from a registered user).
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get the client that owns the message (alias for user relationship).
     */
    public function client()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * Get the parent message (for replies).
     */
    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    /**
     * Get the child messages (replies to this message).
     */
    public function replies()
    {
        return $this->hasMany(static::class, 'parent_id')->orderBy('created_at');
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
        // If this message has a parent, get the root message
        $rootMessage = $this->parent_id ? $this->parent : $this;
        
        // Get all messages in this thread (root + all its replies)
        return static::where('id', $rootMessage->id)
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
        return !is_null($this->parent_id);
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
}