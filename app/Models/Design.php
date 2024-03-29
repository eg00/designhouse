<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\Likeable;
use Cviebrock\EloquentTaggable\Taggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

/**
 * Class Design
 *
 * @method retag($tags)
 */
class Design extends Model
{
    use HasFactory;
    use Likeable;
    use Taggable;

    protected $fillable = [
        'user_id',
        'team_id',
        'image',
        'title',
        'description',
        'slug',
        'close_to_comment',
        'is_live',
        'upload_successful',
        'disk',
    ];

    protected $appends = ['images'];

    //    public function resolveRouteBinding($value, $field = null)
    //    {
    //        return  self::query()->where('slug', $value)->orWhere('id', $value)->firstOrFail();
    //    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array<string>
     */
    public function getImagesAttribute(): array
    {
        return [
            'thumbnail' => $this->getImagePath('thumbnail'),
            'original' => $this->getImagePath('original'),
            'large' => $this->getImagePath('large'),
        ];
    }

    protected function getImagePath(string $size): string
    {
        return Storage::disk($this->disk)
            ->url("uploads/designs/{$size}/{$this->image}");
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')
            ->orderBy('created_at', 'asc');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
