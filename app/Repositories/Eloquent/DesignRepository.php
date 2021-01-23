<?php


namespace App\Repositories\Eloquent;


use App\Models\Design;
use App\Repositories\Contracts\DesignInterface;
use Illuminate\Http\Request;

class DesignRepository extends BaseRepository implements DesignInterface
{

    public function model()
    {
        return Design::class;
    }

    public function ApplyTags($id, array $data)
    {
        $design = $this->find($id);
        $design->retag($data);
    }

    public function addComment($design_id, array $data)
    {
        $design = $this->find($design_id);

        return $design->comments()->create($data);
    }

    public function like($id)
    {
        $design = $this->find($id);

        $design->isLikedByUser() ? $design->unlike() : $design->like();
    }

    public function isLikedByUser($id)
    {
        $design = $this->find($id);

        return $design->isLikedByUser(auth()->id());
    }

    public function search(Request $request)
    {
        $query = (new $this->model)->newQuery();

        $query->where('is_live', true);

        // return only designs with comments
        if ($request->has_comments) {
            $query->has('comments');
        }

        // return only designs assigned to teams
        if ($request->has_team) {
            $query->has('team');
        }

        // search title and description for provided string
        if ($request->q) {
            $query->where(fn($q) => $q->where('title', 'like', '%'.$request->q.'%')
                ->orWhere('description', 'like', '%'.$request->q.'%')
            );
        }

        // order the query by likes or latest first
        if ($request->orderBy === 'likes') {
            $query->withCount('likes')->orderByDesc('likes_count');
        } else {
            $query->latest();
        }

        return $query->get();
    }
}
