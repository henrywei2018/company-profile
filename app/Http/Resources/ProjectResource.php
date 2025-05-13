<?php

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
            'description' => $this->description,
            'short_description' => $this->getShortDescription(),
            'category' => $this->category,
            'client_name' => $this->client_name,
            'location' => $this->location,
            'year' => $this->year,
            'status' => $this->status,
            'featured' => (bool) $this->featured,
            'value' => $this->value,
            'duration' => $this->getDuration(),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'challenge' => $this->challenge,
            'solution' => $this->solution,
            'result' => $this->result,
            'services_used' => $this->services_used,
            'images' => $this->when($this->relationLoaded('images'), function () {
                return $this->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'url' => asset('storage/' . $image->image_path),
                        'alt_text' => $image->alt_text,
                        'is_featured' => (bool) $image->is_featured,
                    ];
                });
            }),
            'featured_image' => $this->getFeaturedImage(),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
    
    /**
     * Get short description
     *
     * @return string
     */
    protected function getShortDescription()
    {
        return Str::limit(strip_tags($this->description), 200);
    }
    
    /**
     * Get featured image URL
     *
     * @return string|null
     */
    protected function getFeaturedImage()
    {
        if ($this->relationLoaded('images')) {
            $featuredImage = $this->images->firstWhere('is_featured', true) ?? $this->images->first();
            
            if ($featuredImage) {
                return asset('storage/' . $featuredImage->image_path);
            }
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