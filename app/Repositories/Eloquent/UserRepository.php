<?php


namespace App\Repositories\Eloquent;


use App\Models\User;
use App\Repositories\Contracts\UserInterface;

class UserRepository implements UserInterface
{

    public function all()
    {
        return User::all();
    }
}
