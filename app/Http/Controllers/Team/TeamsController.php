<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Repositories\Contracts\InvitationInterface;
use App\Repositories\Contracts\TeamInterface;
use App\Repositories\Contracts\UserInterface;
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
     *
     * @param  Request  $request
     * @return TeamResource
     */
    public function store(Request $request): TeamResource
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:80', 'unique:teams,name']
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
     * @return TeamResource
     */
    public function findById($id): TeamResource
    {
        $team = $this->teams->find($id);
        return new TeamResource($team);
    }

    public function findBySlug($slug): TeamResource
    {
        $team = $this->teams->findWhereFirst('slug', $slug);
        return new TeamResource($team);
    }

    /**
     * @return ResourceCollection
     */
    public function fetchUserTeams(): ResourceCollection
    {
        $teams = $this->teams->fetchUserTeams();

        return TeamResource::collection($teams);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return TeamResource
     */
    public function update(Request $request, $id): TeamResource
    {
        $team = $this->teams->find($id);
        $this->authorize('update', $team);

        $this->validate($request, [
            'name' => ['required', 'string', 'max:80', 'unique:teams,name,'.$id]
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
     * @return void
     */
    public function destroy($id)
    {
        //
    }


    public function removeFromTeam($teamId, $userId)
    {
        $team = $this->teams->find($teamId);
        $user = $this->users->find($userId);

        if (optional($user)->isOwnerOfTeam($team)) {
            return response()->json([
                'message' => 'You are the team owner'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (!optional(auth()->user())->isOwnerOfTeam($team)) {
            return response()->json([
                'message' => 'You cannot do this'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $this->invitations->removeUserFromTeam($team, $userId);

        return \response()->json(['message' => 'Successful']);
    }
}
