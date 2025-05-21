<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\MessageTrait;

class Message extends Model
{
    use HasFactory, MessageTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'client_id',
        'parent_id',
        'is_read',
        'read_at',
        'ip_address',
        'user_agent',
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
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'date_formatted',
        'short_message',
    ];

    /**
     * Get the formatted date attribute.
     *
     * @return string
     */
    public function getDateFormattedAttribute()
    {
        return $this->created_at->format('M d, Y H:i');
    }

    /**
     * Get the short message attribute.
     *
     * @return string
     */
    public function getShortMessageAttribute()
    {
        return \Illuminate\Support\Str::limit(strip_tags($this->message), 100);
    }

    /**
     * Save a new message attachment.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return \App\Models\MessageAttachment
     */
    public function addAttachment($file)
    {
        $path = $file->store('message-attachments', 'public');
        
        return $this->attachments()->create([
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);
    }
}