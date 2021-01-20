<?php


namespace App\Repositories\Contracts;


interface InvitationInterface extends BaseInterface
{
    public function addUserToTeam($team, $user_id);
    public function removeUserFromTeam($team, $user_id);
}
