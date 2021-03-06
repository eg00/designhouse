<?php

namespace App\Http\Controllers\Design;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Repositories\Contracts\CommentInterface;
use App\Repositories\Contracts\DesignInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    protected $comments;
    protected $designs;

    /**
     * CommentController constructor.
     * @param $comments
     */
    public function __construct(CommentInterface $comments, DesignInterface $designs)
    {
        $this->comments = $comments;
        $this->designs = $designs;
    }

    public function store(Request $request, $design_id)
    {
        $this->validate($request, [
            'body' => ['required']
        ]);

        $comment = $this->designs->addComment($design_id, [
            'body' => $request->body,
            'user_id' => auth()->id(),
        ]);

        return new CommentResource($comment);
    }

    public function update(Request $request, $id)
    {
        $comment = $this->comments->find($id);
        $this->authorize('update', $comment);

        $this->validate($request, [
            'body' => ['required']
        ]);

        $comment = $this->comments->update($id, [
            'body' => $request->body,
        ]);

        return new CommentResource($comment);
    }


    public function destroy($id)
    {
        $this->comments->delete($id);

        return response()->json(['message' => 'Record deleted'], Response::HTTP_OK);
    }
}
