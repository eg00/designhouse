<?php

namespace App\Http\Controllers\Chats;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;
use App\Repositories\Contracts\ChatInterface;
use App\Repositories\Contracts\MessageInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ChatController extends Controller
{
    public function __construct(protected ChatInterface $chats, protected MessageInterface $messages)
    {
    }

    public function sendMessage(Request $request): MessageResource
    {
        // validate
        $this->validate($request, [
            'recipient' => ['required'],
            'body' => ['required'],
        ]);

        $recipient = $request->input('recipient');
        $user = $request->user();
        $body = $request->input('body');

        $chat = $user->getChatWithUser($recipient);

        if (!$chat) {
            $chat = $this->chats->create([]);
            $this->chats->createParticipants($chat->id, [$user->id, $recipient]);
        }

        // add the message to the chat

        $message = $this->messages->create([
            'user_id' => $user->id,
            'chat_id' => $chat->id,
            'body' => $body
        ]);

        return new MessageResource($message);
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function getUserChats(): AnonymousResourceCollection
    {
        $chats = $this->chats->getUserChats();

        return ChatResource::collection($chats);
    }

    /**
     * @param  int  $id
     */
    public function getChatMessages(int $id)
    {
    }

    /**
     *
     */
    public function markAsRead()
    {
    }

    /**
     *
     */
    public function destroyMessage(): void
    {
    }
}
