<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int id
 */
class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'owner_id',
        'slug',
    ];

    protected static function boot()
    {
        parent::boot();

        // when team is created, add current user as team member
        static::created(fn($team) => $team->members()->attach(auth()->id()));

        static::deleting(fn($team) => $team->members()->sync([]));
    }

    /**
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return HasMany
     */
    public function designs(): HasMany
    {
        return $this->hasMany(Design::class);
    }

    /**
     * @param  User  $user
     * @return bool
     */
    public function hasUser(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)
            ->first() ? true : false;
    }

    /**
     * @return BelongsToMany
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * @param $email
     * @return bool
     */
    public function hasPendingInvite($email): bool
    {
        return (bool) $this->invitation()
            ->where('recipient_email', $email)
            ->count();
    }

    /**
     * @return HasMany
     */
    public function invitation(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }
}
