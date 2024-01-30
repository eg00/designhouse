<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Message;
use App\Repositories\Contracts\MessageInterface;
use Illuminate\Database\Eloquent\Model;

class MessageRepository extends BaseRepository implements MessageInterface
{
    public function model(): Model
    {
        return new Message();
    }
}
