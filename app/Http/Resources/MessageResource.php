<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Message
 */
class MessageResource extends JsonResource
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
            'body' => $this->body,
            'deleted' => $this->trashed(),
            'dates' => [
                'created_at_human' => $this->created_at?->diffForHumans(),
                'created_at' => $this->created_at,
            ],
            'sender' => new UserResource($this->sender),
        ];
    }
}
