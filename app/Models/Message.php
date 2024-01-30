<?php

namespace App\Models;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'chat_id', 'body', 'last_read',
    ];

    protected $touches = ['chat'];

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getBodyAttribute($value)
    {
        if ($this->trashed()) {
            if (! auth()->check()) {
                return null;
            }

            return auth()->id() === $this->sender->id ?
                'You deleted this message' :
                "{$this->sender->name} deleted message";
        }

        return $value;
    }
}
