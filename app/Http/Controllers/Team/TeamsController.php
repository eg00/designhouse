<?php

declare(strict_types=1);

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use App\Models\User;
use App\Repositories\Contracts\InvitationInterface;
use App\Repositories\Contracts\TeamInterface;
use App\Repositories\Contracts\UserInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class TeamsController extends Controller
{
    public function __construct(
        protected TeamInterface $teams,
        protected UserInterface $users,
        protected InvitationInterface $invitations
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): TeamResource
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:80', 'unique:teams,name'],
        ]);

        $team = $this->teams->create([
            'owner_id' => auth()->id(),
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
        ]);

        return new TeamResource($team);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function findById($id): TeamResource
    {
        $team = $this->teams->find($id);

        return new TeamResource($team);
    }

    public function findBySlug(string $slug): TeamResource
    {
        $team = $this->teams->findWhereFirst('slug', $slug);

        return new TeamResource($team);
    }

    public function fetchUserTeams(): ResourceCollection
    {
        $teams = $this->teams->fetchUserTeams();

        return TeamResource::collection($teams);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     */
    public function update(Request $request, $id): TeamResource
    {
        $team = $this->teams->find($id);
        $this->authorize('update', $team);

        $this->validate($request, [
            'name' => ['required', 'string', 'max:80', 'unique:teams,name,'.$id],
        ]);

        $team = $this->teams->update($id, [
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
        ]);

        return new TeamResource($team);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id): void
    {
        //
    }

    public function removeFromTeam(int $teamId, int $userId): JsonResponse
    {
        /** @var Team $team */
        $team = $this->teams->find($teamId);
        /** @var User $user */
        $user = $this->users->find($userId);

        if (optional($user)->isOwnerOfTeam($team)) {
            return response()->json([
                'message' => 'You are the team owner',
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (! optional(auth()->user())->isOwnerOfTeam($team)) {
            return response()->json([
                'message' => 'You cannot do this',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $this->invitations->removeUserFromTeam($team, $userId);

        return \response()->json(['message' => 'Successful']);
    }
}
