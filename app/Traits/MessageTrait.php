<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait MessageTrait
{
    /**
     * Mark the message as read.
     *
     * @return bool
     */
    public function markAsRead()
    {
        return $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Mark the message as unread.
     *
     * @return bool
     */
    public function markAsUnread()
    {
        return $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Toggle read status.
     *
     * @return bool
     */
    public function toggleReadStatus()
    {
        if ($this->is_read) {
            return $this->markAsUnread();
        }
        
        return $this->markAsRead();
    }

    /**
     * Scope a query to only include read messages.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRead(Builder $query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope a query to only include unread messages.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnread(Builder $query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope a query to only include contact form messages.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeContactForm(Builder $query)
    {
        return $query->where('type', 'contact_form');
    }

    /**
     * Scope a query to only include client messages.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeClientMessages(Builder $query)
    {
        return $query->where('type', 'client_to_admin');
    }

    /**
     * Scope a query to only include admin messages.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdminMessages(Builder $query)
    {
        return $query->where('type', 'admin_to_client');
    }

    /**
     * Scope a query to filter by sender/recipient.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $email
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByEmail(Builder $query, $email)
    {
        return $query->where('email', $email);
    }

    /**
     * Scope a query to search messages.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch(Builder $query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('subject', 'like', "%{$search}%")
                ->orWhere('message', 'like', "%{$search}%");
        });
    }

    /**
     * Get client user if the message is from a client.
     */
    public function client()
    {
        return $this->belongsTo(\App\Models\User::class, 'client_id');
    }

    /**
     * Get attachments for the message.
     */
    public function attachments()
    {
        return $this->hasMany(\App\Models\MessageAttachment::class);
    }

    /**
     * Get parent message if this is a reply.
     */
    public function parent()
    {
        return $this->belongsTo(\App\Models\Message::class, 'parent_id');
    }

    /**
     * Get replies to this message.
     */
    public function replies()
    {
        return $this->hasMany(\App\Models\Message::class, 'parent_id');
    }

    /**
     * Get all messages in the same thread.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getThreadMessages()
    {
        // If this message has a parent, start from the parent
        $rootMessage = $this->parent_id ? $this->parent : $this;
        
        // Get all messages in this thread (parent and all its replies)
        return static::where(function ($query) use ($rootMessage) {
            $query->where('id', $rootMessage->id)
                ->orWhere('parent_id', $rootMessage->id);
        })
        ->orderBy('created_at', 'desc')
        ->get();
    }

    /**
     * Check if the message is a reply.
     *
     * @return bool
     */
    public function isReply()
    {
        return !is_null($this->parent_id);
    }

    /**
     * Check if the message has replies.
     *
     * @return bool
     */
    public function hasReplies()
    {
        return $this->replies()->count() > 0;
    }

    /**
     * Check if the message is from contact form.
     *
     * @return bool
     */
    public function isContactForm()
    {
        return $this->type === 'contact_form';
    }

    /**
     * Check if the message is from client to admin.
     *
     * @return bool
     */
    public function isClientToAdmin()
    {
        return $this->type === 'client_to_admin';
    }

    /**
     * Check if the message is from admin to client.
     *
     * @return bool
     */
    public function isAdminToClient()
    {
        return $this->type === 'admin_to_client';
    }
}