<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Team;

interface InvitationInterface extends BaseInterface
{
    public function addUserToTeam(Team $team, int $user_id): void;

    public function removeUserFromTeam(Team $team, int $user_id): void;
}
