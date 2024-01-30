<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\Like;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Likeable
{
    public static function bootLikeable(): void
    {
        static::deleting(fn ($model) => $model->removeLikes());
    }

    public function removeLikes(): void
    {
        if ($this->likes()->count()) {
            $this->likes()->delete();
        }
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function like(): void
    {
        if (! auth()->check()) {
            return;
        }
        if ($this->isLikedByUser()) {
            return;
        }

        $this->likes()->create(['user_id' => auth()->id()]);
    }

    public function isLikedByUser(): bool
    {
        return (bool) $this->likes()->where('user_id', auth()->id())->count();
    }

    public function unlike(): void
    {
        if (! auth()->check()) {
            return;
        }
        if (! $this->isLikedByUser()) {
            return;
        }

        $this->likes()->where('user_id', auth()->id())->delete();
    }
}
