<?php

declare(strict_types=1);

namespace App\Http\Controllers\Chats;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\User;
use App\Repositories\Contracts\ChatInterface;
use App\Repositories\Contracts\MessageInterface;
use App\Repositories\Eloquent\Criteria\WithTrashed;
use Illuminate\Http\JsonResponse;
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
        /** @var User $user */
        $user = $request->user();
        $body = $request->input('body');

        $chat = $user->getChatWithUser($recipient);

        if (! $chat) {
            /** @var Chat $chat */
            $chat = $this->chats->create([]);
            $this->chats->createParticipants((int) $chat->id, [$user->id, $recipient]);
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

    public function markAsRead(int $id): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        /** @var Chat $chat */
        $chat = $this->chats->find($id);
        $chat->markAsReadForUser($user->id);

        return response()->json(['message' => 'successfully']);
    }

    public function destroyMessage(int $id): void
    {
        $message = $this->messages->find($id);
        $this->authorize('delete', $message);

        $message->delete();
    }
}
