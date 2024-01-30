<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Chat
 */
class ChatResource extends JsonResource
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
            'dates' => [
                'created_at_human' => $this->created_at?->diffForHumans(),
                'created_at' => $this->created_at,
            ],
            'is_unread' => $this->isUnreadForUser((int) auth()->id()),
            'latest_message' => new MessageResource($this->latest_message),
            'participants' => UserResource::collection($this->participants),
        ];
    }
}
