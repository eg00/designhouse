<?php

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Repositories\Contracts\TeamInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeamsController extends Controller
{
    public function __construct(protected TeamInterface $teams)
    {
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
            'name'=> ['required', 'string', 'max:80', 'unique:teams,name']
        ]);

        $team = $this->teams->create([
            'owner_id' => auth()->id(),
            'name'=> $request->input('name'),
            'slug'=> Str::slug($request->input('name')),
        ]);

        return  new TeamResource($team);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return void
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        //
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
}
