<?php
// File: app/Http/Resources/PostResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PostResource extends JsonResource
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
            'excerpt' => $this->excerpt,
            'content' => $this->when($request->routeIs('api.posts.show'), $this->content),
            'content_preview' => $this->when(!$request->routeIs('api.posts.show'), Str::limit(strip_tags($this->content), 200)),
            'status' => $this->status,
            'featured' => (bool) $this->featured,
            'published_at' => $this->published_at ? $this->published_at->format('Y-m-d H:i:s') : null,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'featured_image' => $this->when($this->featured_image, function() {
                return asset('storage/' . $this->featured_image);
            }),
            'author' => $this->whenLoaded('author', function() {
                return [
                    'id' => $this->author->id,
                    'name' => $this->author->name,
                    'avatar' => $this->author->getAvatarUrlAttribute(),
                ];
            }),
            'categories' => $this->whenLoaded('categories', function() {
                return $this->categories->map(function($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                    ];
                });
            }),
            'reading_time' => $this->getReadingTime(),
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
     * Calculate estimated reading time
     * 
     * @return int
     */
    protected function getReadingTime()
    {
        // Calculate reading time based on 200 words per minute
        $wordCount = str_word_count(strip_tags($this->content));
        $minutes = ceil($wordCount / 200);
        
        return $minutes > 0 ? $minutes : 1;
    }
}