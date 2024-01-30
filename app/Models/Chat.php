<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'participants');
    }

    public function getLatestMessageAttribute(): ?Message
    {
        return $this->messages()->latest()->first();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function isUnreadForUser(int $user_id): bool
    {
        return (bool) $this->messages()
            ->whereNull('last_read')
            ->where('user_id', '<>', $user_id)
            ->count();
    }

    public function markAsReadForUser(int $user_id): int
    {
        return $this->messages()
            ->whereNull('last_read')
            ->where('user_id', $user_id)
            ->update(['last_read' => Carbon::now()]);
    }
}
