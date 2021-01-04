<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\Contracts\UserInterface;

class UserController extends Controller
{
    protected $users;

    public function __construct(UserInterface $users)
    {
        $this->users = $users;
    }

    public function index()
    {
        $users = User::all();

        return UserResource::collection($users);
    }
}
