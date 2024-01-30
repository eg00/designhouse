<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Comment;
use App\Models\Design;
use App\Repositories\Contracts\DesignInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DesignRepository extends BaseRepository implements DesignInterface
{
    public function model(): Model|Builder
    {
        return new Design();
    }

    /**
     * @param  array<string>  $data
     */
    public function ApplyTags(int $id, array $data): void
    {
        /** @var Design $design */
        $design = $this->find($id);
        $design->retag($data);
    }

    /**
     * @param  array<mixed>  $data
     */
    public function addComment(int $design_id, array $data): Comment
    {
        /** @var Design $design */
        $design = $this->find($design_id);

        return $design->comments()->create($data);
    }

    public function like(int $id): void
    {
        /** @var Design $design */
        $design = $this->find($id);

        $design->isLikedByUser() ? $design->unlike() : $design->like();
    }

    public function isLikedByUser(int $design_id): bool
    {
        /** @var Design $design */
        $design = $this->find($design_id);

        return $design->isLikedByUser();
    }

    public function search(Request $request): Collection
    {
        $query = $this->model()->newQuery();

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
            $query->where(fn ($q) => $q->where('title', 'like', '%'.$request->q.'%')
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
