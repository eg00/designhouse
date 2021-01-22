<?php


namespace App\Repositories\Eloquent;


use App\Models\Chat;
use App\Repositories\Contracts\ChatInterface;

class ChatRepository extends BaseRepository implements ChatInterface
{
    public function model()
    {
        return Chat::class;
    }
}
