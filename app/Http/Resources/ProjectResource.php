<?php
// File: app/Http/Resources/ProjectResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ProjectResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->when($request->routeIs('api.projects.show'), $this->description),
            'description_preview' => $this->when(!$request->routeIs('api.projects.show'), 
                Str::limit(strip_tags($this->description), 200)
            ),
            'category' => $this->category,
            'client_name' => $this->client_name,
            'location' => $this->location,
            'year' => $this->year,
            'status' => $this->status,
            'value' => $this->value,
            'featured' => (bool) $this->featured,
            'start_date' => $this->start_date ? $this->start_date->format('Y-m-d') : null,
            'end_date' => $this->end_date ? $this->end_date->format('Y-m-d') : null,
            'duration' => $this->getDuration(),
            'challenge' => $this->when($request->routeIs('api.projects.show'), $this->challenge),
            'solution' => $this->when($request->routeIs('api.projects.show'), $this->solution),
            'result' => $this->when($request->routeIs('api.projects.show'), $this->result),
            'services_used' => $this->services_used,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'images' => $this->whenLoaded('images', function() {
                return $this->images->map(function($image) {
                    return [
                        'id' => $image->id,
                        'url' => asset('storage/' . $image->image_path),
                        'alt_text' => $image->alt_text,
                        'is_featured' => (bool) $image->is_featured,
                        'sort_order' => $image->sort_order,
                    ];
                });
            }),
            'featured_image' => $this->getFeaturedImageUrl(),
            'client' => $this->whenLoaded('client', function() {
                return [
                    'id' => $this->client->id,
                    'name' => $this->client->name,
                    'company' => $this->client->company,
                ];
            }),
            'testimonial' => $this->whenLoaded('testimonial', function() {
                return [
                    'id' => $this->testimonial->id,
                    'client_name' => $this->testimonial->client_name,
                    'client_position' => $this->testimonial->client_position,
                    'client_company' => $this->testimonial->client_company,
                    'content' => $this->testimonial->content,
                    'rating' => $this->testimonial->rating,
                    'image' => $this->testimonial->image ? asset('storage/' . $this->testimonial->image) : null,
                ];
            }),
            'seo' => $this->whenLoaded('seo', function() {
                return [
                    'title' => $this->seo->title,
                    'description' => $this->seo->description,
                    'keywords' => $this->seo->keywords,
                ];
            }),
        ];
    }
    
    /**
     * Get featured image URL
     * 
     * @return string|null
     */
    protected function getFeaturedImageUrl()
    {
        if (!$this->relationLoaded('images')) {
            return null;
        }
        
        $featuredImage = $this->images->firstWhere('is_featured', true) ?? $this->images->first();
        
        if ($featuredImage) {
            return asset('storage/' . $featuredImage->image_path);
        }
        
        return null;
    }
    
    /**
     * Get project duration in months
     * 
     * @return int|null
     */
    protected function getDuration()
    {
        if ($this->start_date && $this->end_date) {
            $startDate = new \DateTime($this->start_date);
            $endDate = new \DateTime($this->end_date);
            $interval = $startDate->diff($endDate);
            
            // Calculate total months
            return $interval->y * 12 + $interval->m;
        }
        
        return null;
    }
}