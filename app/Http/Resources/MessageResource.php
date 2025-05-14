<?php
// File: app/Http/Resources/MessageResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => $this->company,
            'subject' => $this->subject,
            'message' => $this->message,
            'type' => $this->type,
            'is_read' => (bool) $this->is_read,
            'is_read_by_client' => (bool) $this->is_read_by_client,
            'read_at' => $this->read_at ? $this->read_at->format('Y-m-d H:i:s') : null,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'parent_id' => $this->parent_id,
            'project_id' => $this->project_id,
            'client' => $this->whenLoaded('client', function() {
                return [
                    'id' => $this->client->id,
                    'name' => $this->client->name,
                    'email' => $this->client->email,
                ];
            }),
            'project' => $this->whenLoaded('project', function() {
                return [
                    'id' => $this->project->id,
                    'title' => $this->project->title,
                    'slug' => $this->project->slug,
                ];
            }),
            'attachments' => $this->whenLoaded('attachments', function() {
                return $this->attachments->map(function($attachment) {
                    return [
                        'id' => $attachment->id,
                        'file_name' => $attachment->file_name,
                        'file_size' => $attachment->file_size,
                        'file_type' => $attachment->file_type,
                        'formatted_file_size' => $attachment->getFormattedFileSizeAttribute(),
                        'file_icon' => $attachment->getFileIconAttribute(),
                    ];
                });
            }),
            'replies_count' => $this->when(
                $this->relationLoaded('replies'), 
                $this->replies->count(), 
                function() {
                    return $this->replies()->count();
                }
            ),
        ];
    }
}