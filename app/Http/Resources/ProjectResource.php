<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'category' => $this->category,
            'location' => $this->location,
            'client_name' => $this->client_name,
            'year' => $this->year,
            'status' => $this->status,
            'featured' => $this->featured,
            'featured_image_url' => $this->featured_image_url,
            'images' => $this->when($this->relationLoaded('images'), function () {
                return $this->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'url' => $image->image_url,
                        'alt_text' => $image->alt_text,
                    ];
                });
            }),
            'testimonial' => $this->when($this->relationLoaded('testimonial'), function () {
                return $this->testimonial ? [
                    'client_name' => $this->testimonial->client_name,
                    'content' => $this->testimonial->content,
                    'rating' => $this->testimonial->rating,
                ] : null;
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}