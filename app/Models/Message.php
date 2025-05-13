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
     * @var array
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
        'read_at',
        'client_id',
        'is_read_by_client',
        'parent_id',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_read' => 'boolean',
        'is_read_by_client' => 'boolean',
        'read_at' => 'datetime',
    ];
    
    /**
     * Get the user associated with the message.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the client associated with the message.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
    
    /**
     * Get the parent message.
     */
    public function parent()
    {
        return $this->belongsTo(Message::class, 'parent_id');
    }
    
    /**
     * Get the replies to this message.
     */
    public function replies()
    {
        return $this->hasMany(Message::class, 'parent_id');
    }
    
    /**
     * Get message attachments.
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
    
    /**
     * Scope a query to only include unread messages.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
    
    /**
     * Mark message as read.
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
        
        return $this;
    }
}