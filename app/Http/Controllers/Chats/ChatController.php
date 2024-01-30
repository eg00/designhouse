<?php

namespace App\Http\Controllers\Chats;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;
use App\Repositories\Contracts\ChatInterface;
use App\Repositories\Contracts\MessageInterface;
use App\Repositories\Eloquent\Criteria\WithTrashed;
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

        if (! $chat) {
            $chat = $this->chats->create([]);
            $this->chats->createParticipants($chat->id, [$user->id, $recipient]);
        }

        // add the message to the chat

        $message = $this->messages->create([
            'user_id' => $user->id,
            'chat_id' => $chat->id,
            'body' => $body,
        ]);

        return new MessageResource($message);
    }

    public function getUserChats(): AnonymousResourceCollection
    {
        $chats = $this->chats->getUserChats();

        return ChatResource::collection($chats);
    }

    public function getChatMessages(string $id): AnonymousResourceCollection
    {
        $messages = $this->messages->withCriteria([
            new WithTrashed(),
        ])->findWhere('chat_id', $id);

        return MessageResource::collection($messages);
    }

    public function markAsRead($id)
    {
        $chat = $this->chats->find($id);
        $chat->markAsReadForUser(auth()->id());

        return response()->json(['message' => 'successfully']);
    }

    public function destroyMessage($id)
    {
        $message = $this->messages->find($id);
        $this->authorize('delete', $message);

        $message->delete();
    }
}
