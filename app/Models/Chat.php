<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Chat extends Model
{
    use HasFactory;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'participants');
    }

    public function getLatestMessageAttribute()
    {
        return $this->messages()->latest()->first();
    }

    // helper

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function isUnreadForUser($user_id)
    {
        return (boolean) $this->messages()
            ->whereNull('last_read')
            ->where('user_id', '<>', $user_id)
            ->count();
    }

    public function markAsReaadForUser($user_id)
    {
        return $this->messages()
            ->whereNull('last_read')
            ->where('user_id', '<>', $user_id)
            ->update(['last_read' => Carbon::now()]);
    }
}
