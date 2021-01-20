<?php


namespace App\Repositories\Eloquent;


use App\Models\Invitation;
use App\Repositories\Contracts\InvitationInterface;

class InvitationRepository extends BaseRepository implements InvitationInterface
{
    public function model()
    {
        return Invitation::class;
    }

}
