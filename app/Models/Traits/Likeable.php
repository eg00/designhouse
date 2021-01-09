<?php


namespace App\Models\Traits;


use App\Models\Like;

trait Likeable
{
    public static function bootLikeable()
    {
        static::deleting(fn ($model) => $model->removeLikes());
    }

    public function removeLikes()
    {
        if ($this->likes()->count()) {
            $this->likes()->delete();
        }
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function like()
    {
        if (!auth()->check()) {
            return;
        }
        if ($this->isLikedByUser()) {
            return;
        }

        $this->likes()->create(['user_id' => auth()->id()]);
    }

    public function isLikedByUser(): bool
    {
        return $this->likes()->where('user_id', auth()->id())->count();
    }

    public function unlike()
    {
        if (!auth()->check()) {
            return;
        }
        if (!$this->isLikedByUser()) {
            return;
        }

        $this->likes()->where('user_id', auth()->id())->delete();
    }
}
