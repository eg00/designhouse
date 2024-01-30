<?php

namespace App\Repositories\Eloquent;

use App\Models\Message;
use App\Repositories\Contracts\MessageInterface;

class MessageRepository extends BaseRepository implements MessageInterface
{
    public function model()
    {
        return Message::class;
    }
}
