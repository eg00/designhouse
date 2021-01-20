<?php


namespace App\Repositories\Eloquent;


use App\Models\User;
use App\Repositories\Contracts\UserInterface;

class UserRepository extends BaseRepository implements UserInterface
{


    public function model()
    {
        return User::class;
    }

    public function findByEmail(string $email)
    {
        return $this->findWhere('email', $email)->first();
    }
}
