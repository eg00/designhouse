<?php

declare(strict_types=1);

namespace App\Models;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'chat_id', 'body', 'last_read',
    ];

    /**
     * The relationships that should be touched on save.
     *
     * @var array<string>
     */
    protected $touches = ['chat'];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getBodyAttribute(string $value): ?string
    {
        if ($this->trashed()) {
            if (! auth()->check()) {
                return null;
            }

            /** @var User $sender */
            $sender = $this->sender;

            return auth()->id() === $sender->id ?
                'You deleted this message' :
                "{$sender->name} deleted message";
        }

        return $value;
    }
}
