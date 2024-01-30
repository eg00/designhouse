<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Like extends Model
{
    protected $fillable = [
        'user_id',

    ];

    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }
}
