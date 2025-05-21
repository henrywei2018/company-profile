<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait MessageTrait
{
    /**
     * Get messages from the client.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromClient($query)
    {
        return $query->where('type', 'client_to_admin');
    }

    /**
     * Get messages from the admin.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromAdmin($query)
    {
        return $query->where('type', 'admin_to_client');
    }

    /**
     * Get unread messages.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Get read messages.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Get contact form messages.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeContactForm($query)
    {
        return $query->where('type', 'contact_form');
    }

    /**
     * Search messages.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
        });
    }

    /**
     * Mark the message as read.
     *
     * @return $this
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return $this;
    }

    /**
     * Toggle the read status of the message.
     *
     * @return $this
     */
    public function toggleReadStatus()
    {
        $this->update([
            'is_read' => !$this->is_read,
            'read_at' => !$this->is_read ? now() : null,
        ]);

        return $this;
    }

    /**
     * Get the client that owns the message.
     */
    public function client()
    {
        return $this->belongsTo(\App\Models\User::class, 'client_id');
    }

    /**
     * Get the parent message.
     */
    public function parent()
    {
        return $this->belongsTo(\App\Models\Message::class, 'parent_id');
    }

    /**
     * Get the reply messages.
     */
    public function replies()
    {
        return $this->hasMany(\App\Models\Message::class, 'parent_id');
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments()
    {
        return $this->hasMany(\App\Models\MessageAttachment::class);
    }

    /**
     * Get all messages in the same thread.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getThreadMessages()
    {
        $rootId = $this->parent_id ?? $this->id;
        
        // If this is a reply, get the root message
        if ($this->parent_id) {
            $rootMessage = $this->parent;
            while ($rootMessage && $rootMessage->parent_id) {
                $rootMessage = $rootMessage->parent;
                $rootId = $rootMessage->id;
            }
        }
        
        return static::where(function($query) use ($rootId) {
            $query->where('id', $rootId)
                  ->orWhere('parent_id', $rootId);
        })->orWhere(function($query) {
            $query->where('id', $this->id)
                  ->orWhere('parent_id', $this->id);
        })->orderBy('created_at', 'desc')->get();
    }
}