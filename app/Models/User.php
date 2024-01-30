<?php

declare(strict_types=1);

namespace App\Models;

use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use Carbon\CarbonImmutable;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravolt\Avatar\Avatar;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $email
 * @property CarbonImmutable|null $email_verified_at
 * @property string|null $tagline
 * @property string|null $about
 * @property bool $available_to_hire
 * @property string $password
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Avatar                 $avatar
 * @property-read Collection<Team>       $teams
 * @property-read Collection<Invitation> $invitations
 * @property-read Collection<Message>    $messages
 * @property-read Collection<Design>     $designs
 * @property-read Collection<Comment>    $comments
 * @property-read Collection<Chat>       $chats
 */
class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasFactory, HasSpatial, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * * @var array<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tagline',
        'about',
        'username',
        'location',
        'available_to_hire',
        'formatted_address',
    ];

    /**
     * @var string[]
     */
    protected $spatialFields = [
        'location',
    ];

    /**
     * @return string[]
     */
    public function getHidden()
    {
        return [
            'password',
            'remember_token',
        ];
    }

    /**
     * @return string[]
     */
    public function getCasts()
    {
        return [
            'email_verified_at' => 'immutable_datetime',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    /**
     * @return array<string>
     */
    public function getAppends(): array
    {
        return ['photo_url'];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array<mixed>
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPassword($token));
    }

    public function designs(): HasMany
    {
        return $this->hasMany(Design::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function ownedTeams(): BelongsToMany
    {
        return $this->teams()->where('owner_id', $this->id);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)->withTimestamps();
    }

    public function isOwnerOfTeam(Team $team): bool
    {
        return (bool) $this->teams()
            ->where('team_id', $team->id)
            ->where('owner_id', $this->id)
            ->count();
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class, 'recipient_email', 'email');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function getChatWithUser(int $user_id): Chat
    {
        return $this->chats()->whereHas('participants', fn ($query) => $query->where('user_id', $user_id)
        )->firstOrFail();
    }

    public function chats(): BelongsToMany
    {
        return $this->belongsToMany(Chat::class, 'participants');
    }

    public function getPhotoUrlAttribute(): Avatar
    {
        return (new Avatar)->create($this->email)->toGravatar();
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
