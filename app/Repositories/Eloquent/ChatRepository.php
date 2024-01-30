<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Chat;
use App\Models\User;
use App\Repositories\Contracts\ChatInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ChatRepository extends BaseRepository implements ChatInterface
{
    public function model(): Model
    {
        return new Chat();
    }

    /**
     * @param  array<mixed>  $data
     */
    public function createParticipants(int $chat_id, array $data): void
    {
        $chat = $this->model()->find($chat_id);
        assert($chat instanceof Chat);
        $chat->participants()->sync($data);
    }

    public function getUserChats(): Collection
    {
        $user = auth()->user();
        assert($user instanceof User);

        return $user->chats()->with(['messages', 'participants'])->get();
    }
}
