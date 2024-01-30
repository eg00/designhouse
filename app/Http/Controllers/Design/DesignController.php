<?php

declare(strict_types=1);

namespace App\Http\Controllers\Design;

use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use App\Models\Design;
use App\Repositories\Contracts\DesignInterface;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\ForUser;
use App\Repositories\Eloquent\Criteria\IsLive;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DesignController extends Controller
{
    protected DesignInterface $designs;

    public function __construct(DesignInterface $designs)
    {
        $this->designs = $designs;
    }

    public function index(): ResourceCollection
    {
        $designs = $this->designs->withCriteria([
            new LatestFirst(), new IsLive(), new ForUser(1), new EagerLoad(['user', 'comments']),
        ])->all();

        return DesignResource::collection($designs);
    }

    public function show(int $id): DesignResource
    {
        $design = $this->designs->find($id);

        return new DesignResource($design);
    }

    /**
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(Request $request, int $id): DesignResource
    {
        /** @var Design $design */
        $design = $this->designs->find($id);
        $this->authorize('update', $design);

        $this->validate($request, [
            'title' => ['required', 'unique:designs,title,'.$id],
            'description' => ['required', 'string', 'min:20', 'max:140'],
            'tags' => ['required'],
            'team' => ['required_if:assign_to_team,1'],
        ]);

        $design = $this->designs->update($id, [
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'slug' => Str::slug($request->input('title')),
            'is_live' => ! $design->upload_successful ? false : $request->input('is_live'),
            'team_id' => $request->input('team'),
        ]);

        $this->designs->applyTags($id, $request->input('tags'));

        //        return response()->json($design, Response::HTTP_OK);
        return new DesignResource($design);
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(int $id): JsonResponse
    {
        /** @var Design $design */
        $design = $this->designs->find($id);
        $this->authorize('delete', $design);

        //delete the files associated to the record
        foreach (['thumbnail', 'large', 'original'] as $size) {
            $path = "uploads/designs/{$size}/{$design->image}";
            if (Storage::disk($design->disk)->exists($path)) {
                Storage::disk($design->disk)->delete($path);
            }
        }

        $this->designs->delete($id);

        return \response()->json(['message' => 'Record deleted'], Response::HTTP_OK);
    }

    public function like(int $id): JsonResponse
    {
        $this->designs->like($id);

        return \response()->json(['message' => 'Successful'], Response::HTTP_OK);
    }

    public function checkIfUserHasLiked(int $design_id): JsonResponse
    {
        $isLiked = $this->designs->isLikedByUser($design_id);

        return \response()->json(['liked' => $isLiked]);
    }

    public function search(Request $request): ResourceCollection
    {
        $designs = $this->designs->search($request);

        return DesignResource::collection($designs);
    }

    public function showBySlug(string $slug): DesignResource
    {
        $design = $this->designs->withCriteria([
            new LatestFirst(), new IsLive(),
        ])->findWhereFirst('slug', $slug);

        return new DesignResource($design);
    }

    public function getForTeam(int $id): ResourceCollection
    {
        $designs = $this->designs->withCriteria([
            new LatestFirst(), new IsLive(),
        ])->findWhere('team_id', $id);

        return DesignResource::collection($designs);
    }

    public function getForUser(int $id): ResourceCollection
    {
        $designs = $this->designs->withCriteria([
            new LatestFirst(), new IsLive(),
        ])->findWhere('user_id', $id);

        return DesignResource::collection($designs);
    }
}
