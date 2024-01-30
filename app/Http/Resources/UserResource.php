<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\User
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            $this->mergeWhen(auth()->check() && auth()->id() === $this->id, [
                'email' => $this->email,
            ]),
            'name' => $this->name,
            'designs' => DesignResource::collection($this->whenLoaded('designs')),
            'photo_url' => $this->photo_url,
            'created_at_dates' => [
                'created_at_human' => $this->created_at?->diffForHumans(),
                'created_at' => $this->created_at,
            ],
            'formatted_address' => $this->formatted_address,
            'tagline' => $this->tagline,
            'about' => $this->about,
            'location' => $this->location,
            'available_to_hire' => $this->available_to_hire,
        ];
    }
}
