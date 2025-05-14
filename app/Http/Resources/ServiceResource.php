<?php
// File: app/Http/Resources/ServiceResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ServiceResource extends JsonResource
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
            'short_description' => $this->short_description,
            'description' => $this->when($request->routeIs('api.services.show'), $this->description),
            'description_preview' => $this->when(!$request->routeIs('api.services.show'), 
                Str::limit(strip_tags($this->description), 200)
            ),
            'icon' => $this->when($this->icon, function() {
                return asset('storage/' . $this->icon);
            }),
            'image' => $this->when($this->image, function() {
                return asset('storage/' . $this->image);
            }),
            'featured' => (bool) $this->featured,
            'is_active' => (bool) $this->is_active,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'category' => $this->whenLoaded('category', function() {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                    'icon' => $this->category->icon ? asset('storage/' . $this->category->icon) : null,
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
}