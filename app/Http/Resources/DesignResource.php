<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Design
 */
class DesignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->user),
            'title' => $this->title,
            'slug' => $this->slug,
            'images' => $this->images,
            'is_live' => $this->is_live,
            'likes_count' => $this->likes()->count(),
            'description' => $this->description,
            'tag_list' => [
                'normalized' => $this->tagArrayNormalized,
                'tags' => $this->tagArray,
            ],
            'created_at_dates' => [
                'created_at_human' => $this->created_at?->diffForHumans(),
                'created_at' => $this->created_at,
            ],
            'updated_at_dates' => [
                'updated_at_human' => $this->updated_at?->diffForHumans(),
                'updated_at' => $this->updated_at,
            ],
            'team' => $this->team ? [
                'name' => $this->team->name,
                'slug' => $this->team->slug,
            ] : null,
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
        ];
    }
}
