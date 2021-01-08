<?php

namespace App\Models;

use Cviebrock\EloquentTaggable\Taggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Class Design
 * @package App\Models
 * @method retag($tags)
 */
class Design extends Model
{
    use HasFactory;
    use Taggable;

    protected $fillable = [
        'user_id', 'image', 'title', 'description', 'slug', 'close_to_comment', 'is_live', 'upload_successful', 'disk'
    ];
    protected $appends = ['images'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getImagesAttribute()
    {


        return [
            'thumbnail' => $this->getImagePath('thumbnail'),
            'original' => $this->getImagePath('original'),
            'large' => $this->getImagePath('large'),
        ];
    }

    protected function getImagePath(string $size)
    {
        return Storage::disk($this->disk)
            ->url("uploads/designs/{$size}/{$this->image}");
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')
            ->orderBy('created_at', 'asc');
    }
}
