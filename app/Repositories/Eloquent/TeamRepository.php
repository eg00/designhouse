<?php


namespace App\Repositories\Eloquent;


use App\Models\Team;
use App\Repositories\Contracts\TeamInterface;

class TeamRepository extends BaseRepository implements TeamInterface
{

    public function model()
    {
        return Team::class;
    }

    public function fetchUserTeams()
    {
        // TODO: Implement fetchUserTeams() method.
    }

    public function findBySlug($slug)
    {
        // TODO: Implement findBySlug() method.
    }
}
