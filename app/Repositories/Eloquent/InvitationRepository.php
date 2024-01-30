<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Invitation;
use App\Models\Team;
use App\Repositories\Contracts\InvitationInterface;
use Illuminate\Database\Eloquent\Model;

class InvitationRepository extends BaseRepository implements InvitationInterface
{
    public function model(): Model
    {
        return new Invitation();
    }

    public function addUserToTeam(Team $team, int $user_id): void
    {
        $team->members()->attach($user_id);
    }

    public function removeUserFromTeam(Team $team, int $user_id): void
    {
        $team->members()->detach($user_id);
    }
}
