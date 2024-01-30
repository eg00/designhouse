<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property-read User $owner
 * @property-read Collection<User> $members
 * @property-read Collection<Design> $designs
 * @property-read Collection<Invitation> $invitation
 */
class Team extends Model
{
    protected $fillable = [
        'name',
        'owner_id',
        'slug',
    ];

    protected static function boot()
    {
        parent::boot();

        // when team is created, add current user as team member
        static::created(fn ($team) => $team->members()->attach(auth()->id()));

        static::deleting(fn ($team) => $team->members()->sync([]));
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function designs(): HasMany
    {
        return $this->hasMany(Design::class);
    }

    public function hasUser(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)
            ->first() ? true : false;
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function hasPendingInvite(string $email): bool
    {
        return (bool) $this->invitation()
            ->where('recipient_email', $email)
            ->count();
    }

    public function invitation(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }
}
