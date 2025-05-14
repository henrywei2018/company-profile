<?php
// File: app/Http/Resources/QuotationResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuotationResource extends JsonResource
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
            'project_type' => $this->project_type,
            'location' => $this->location,
            'requirements' => $this->requirements,
            'budget_range' => $this->budget_range,
            'start_date' => $this->start_date ? $this->start_date->format('Y-m-d') : null,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'additional_info' => $this->additional_info,
            'client_approved' => (bool) $this->client_approved,
            'client_approved_at' => $this->client_approved_at ? $this->client_approved_at->format('Y-m-d H:i:s') : null,
            'service' => $this->whenLoaded('service', function() {
                return [
                    'id' => $this->service->id,
                    'title' => $this->service->title,
                    'slug' => $this->service->slug,
                ];
            }),
            'client' => $this->whenLoaded('client', function() {
                return [
                    'id' => $this->client->id,
                    'name' => $this->client->name,
                    'email' => $this->client->email,
                    'company' => $this->client->company,
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
        ];
    }
}