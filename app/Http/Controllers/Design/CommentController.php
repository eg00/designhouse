<?php

declare(strict_types=1);

namespace App\Http\Controllers\Design;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Repositories\Contracts\CommentInterface;
use App\Repositories\Contracts\DesignInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    /**
     * CommentController constructor.
     */
    public function __construct(
        protected CommentInterface $comments,
        protected DesignInterface $designs,
    ) {
    }

    public function store(Request $request, int $design_id): CommentResource
    {
        $this->validate($request, [
            'body' => ['required'],
        ]);

        $comment = $this->designs->addComment($design_id, [
            'body' => $request->body,
            'user_id' => auth()->id(),
        ]);

        return new CommentResource($comment);
    }

    public function update(Request $request, int $id): CommentResource
    {
        $comment = $this->comments->find($id);
        $this->authorize('update', $comment);

        $this->validate($request, [
            'body' => ['required'],
        ]);

        $comment = $this->comments->update($id, [
            'body' => $request->body,
        ]);

        return new CommentResource($comment);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->comments->delete($id);

        return response()->json(['message' => 'Record deleted'], Response::HTTP_OK);
    }
}
